const http = require("http");

const OLLAMA_BASE_URL = process.env.OLLAMA_BASE_URL || "http://ollama:11434";
const MODEL = process.env.OLLAMA_MODEL || "phi3:mini";
const MAX_WORDS = parseInt(process.env.MAX_WORDS || "25", 10);
const TEMPERATURE = parseFloat(process.env.TEMPERATURE || "0.2");
const NUM_PREDICT = parseInt(process.env.NUM_PREDICT || "60", 10);
const PORT = parseInt(process.env.PORT || "3000", 10);

function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
}

function readJson(req) {
    return new Promise((resolve, reject) => {
        let body = "";
        req.on("data", (chunk) => {
            body += chunk;
            if (body.length > 256 * 1024) {
                reject(new Error("Payload too large (max 256kb)."));
                req.destroy();
            }
        });
        req.on("end", () => {
            if (!body) return resolve(null);
            try {
                resolve(JSON.parse(body));
            } catch {
                reject(new Error("Invalid JSON body."));
            }
        });
    });
}

function json(res, code, obj) {
    const out = JSON.stringify(obj);
    res.writeHead(code, {
        "Content-Type": "application/json",
        "Content-Length": Buffer.byteLength(out),
    });
    res.end(out);
}

function hasAllPlaceholders(text) {
    return ["{{PET}}", "{{REASON}}", "{{EXAM}}", "{{TREATMENT}}", "{{PLAN}}"]
        .every((p) => text.includes(p));
}

function fillPlaceholders(template, values) {
    return template
        .replaceAll("{{PET}}", values.pet)
        .replaceAll("{{REASON}}", values.reason)
        .replaceAll("{{EXAM}}", values.exam)
        .replaceAll("{{TREATMENT}}", values.treatment)
        .replaceAll("{{PLAN}}", values.plan);
}

function fallbackTemplate(values) {
    return `${values.pet} was seen for ${values.reason} and found to have ${values.exam}, was treated with ${values.treatment}, and the plan is ${values.plan}.`;
}

function buildPlaceholderPrompt() {
    return `
Write EXACTLY ONE sentence using ALL placeholders below exactly as written.
Do not change placeholder text. Do not omit any placeholder.
Do not add or remove medical details.

Tone:
- Friendly, calm, professional
- Clear for a pet owner (not clinical, not robotic)
- Warm but professional, reassuring

Style rules:
- Prefer "was seen for" or "was treated for"
- Use "and" or "; and" before next steps
- Avoid awkward phrasing like "received" at the start
- Do NOT repeat words unnecessarily

Placeholders (ALL required):
{{PET}} {{REASON}} {{EXAM}} {{TREATMENT}} {{PLAN}}

Good example styles:
"{{PET}} was seen for {{REASON}} and found to have {{EXAM}}, was treated with {{TREATMENT}}, and the plan is {{PLAN}}."
"{{PET}} was treated for {{REASON}} with findings of {{EXAM}}, received {{TREATMENT}}, and next steps include {{PLAN}}."

Return ONLY the sentence.
`.trim();
}
async function ollamaChat(prompt) {
    const controller = new AbortController();
    const t = setTimeout(() => controller.abort(), 15000);

    try {
        const res = await fetch(`${OLLAMA_BASE_URL}/api/chat`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            signal: controller.signal,
            body: JSON.stringify({
                model: MODEL,
                stream: false,
                options: {
                    temperature: clamp(TEMPERATURE, 0, 1),
                    num_predict: clamp(NUM_PREDICT, 20, 120),
                },
                messages: [
                    {
                        role: "system",
                        content:
                            `You are a deterministic formatter. Your job is to produce EXACTLY ONE sentence that includes EVERY provided field value.

Hard requirements:
- Output EXACTLY ONE sentence.
- You MUST include the exact text (verbatim) of each provided field value: pet, reason, exam, treatment, plan.
- Do NOT omit, shorten, paraphrase, or generalize any value.
- Do NOT add new information.
- Use commas and semicolons to keep it one sentence.
- Output only the sentence.
`,
                    },
                    { role: "user", content: prompt },
                ],
            }),
        });

        if (!res.ok) {
            const text = await res.text().catch(() => "");
            throw new Error(`Ollama error: ${res.status} ${res.statusText} ${text}`.trim());
        }

        const json = await res.json();
        return (json?.message?.content ?? "").trim();
    } finally {
        clearTimeout(t);
    }
}

function postProcessOneSentence(text) {
    let s = (text || "").trim();
    s = s.replace(/^["'“”‘’]+/, "").replace(/["'“”‘’]+$/, "").trim();
    s = s.replace(/\s+/g, " ").trim();

    // keep first sentence if model outputs more
    const parts = s.split(/(?<=[.!?])\s+/);
    if (parts.length > 1) s = parts[0].trim();

    // enforce word cap
    const words = s.split(" ").filter(Boolean);
    if (words.length > MAX_WORDS) {
        s = words.slice(0, MAX_WORDS).join(" ").replace(/[,:;]\s*$/, "").trim();
    }

    if (!/[.!?]$/.test(s)) s += ".";
    return s;
}

const server = http.createServer(async (req, res) => {
    try {
        const { method, url } = req;

        if (method === "GET" && url === "/health") {
            return json(res, 200, { ok: true, model: MODEL });
        }

        if (method === "POST" && url === "/summary") {
            const body = await readJson(req);

            // ✅ define data ONCE (this is what you forgot)
            const data = body?.data;

            // data is already validated earlier (body?.data)
            if (typeof data === "object" && data !== null) {
                const values = {
                    pet: String(data.pet ?? "").trim(),
                    reason: String(data.reason ?? "").trim(),
                    exam: String(data.exam ?? "").trim(),
                    treatment: String(data.treatment ?? "").trim(),
                    plan: String(data.plan ?? "").trim(),
                };

                // Ask model only for a *template* with placeholders
                const prompt = buildPlaceholderPrompt();
                let raw = await ollamaChat(prompt);

                // Retry once if placeholders missing
                if (!hasAllPlaceholders(raw)) {
                    raw = await ollamaChat(prompt + "\nCRITICAL: Include ALL placeholders exactly; regenerate.");
                }

                // Deterministic injection (guaranteed)
                const sentence = hasAllPlaceholders(raw)
                    ? fillPlaceholders(raw, values)
                    : fallbackTemplate(values);

                const summary = postProcessOneSentence(sentence);
                return json(res, 200, { summary, model: MODEL });
            }
        }

        return json(res, 404, { error: "Not found" });
    } catch (err) {
        return json(res, 500, { error: String(err?.message || err) });
    }
});

server.listen(PORT, () => {
    console.log(`summary-api listening on :${PORT} (model=${MODEL})`);
});

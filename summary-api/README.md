# Visit Summary API (Local LLM, Deterministic)

A lightweight, **CPU-only**, **fully local** service that generates **one-sentence, owner-friendly veterinary visit summaries** using a local LLM (Ollama), with **guaranteed inclusion of all visit details**.

This service is designed for **production safety**:
- No external APIs
- No data loss
- Deterministic output
- Human-readable wording

---

## ✨ Key Features

- ✅ **Runs fully locally** (CPU-only, no GPU required)
- ✅ **One `docker compose up`** — model auto-loaded
- ✅ **Never drops visit details** (server-side enforcement)
- ✅ **Owner-friendly phrasing**
- ✅ **Single-sentence output**
- ✅ **Simple REST API**
- ✅ **No npm / no external build dependencies**

---

## 🧠 Architecture Overview

```

Client
|
|  POST /summary
v
Summary API (Node.js, no dependencies)
|
|  /api/chat
v
Ollama (local LLM)

````

### Why this design?
- The LLM is used **only for phrasing**, not for data integrity.
- All visit details are **injected server-side**, making omissions impossible.
- If the model fails, the API falls back to a deterministic template.

This makes the service safe for **client-facing medical summaries**.

---

## 📦 Services

| Service        | Description |
|---------------|-------------|
| `ollama`       | Local LLM runtime |
| `ollama-init`  | Pulls model automatically on startup |
| `summary-api`  | REST API wrapper enforcing correctness |

---

## 🚀 Quick Start

### 1️⃣ Prerequisites
- Docker
- Docker Compose

### 2️⃣ Start everything
```bash
docker compose up -d --build
````

This will:

* Start Ollama
* Pull the configured model (`phi3:mini`)
* Start the Summary API

No manual steps required.

---

## 🔍 Health Check

```bash
curl http://localhost:3000/health
```

Response:

```json
{
  "ok": true,
  "model": "phi3:mini"
}
```

---

## 🧾 Generate a Visit Summary

### Request

```bash
curl http://localhost:3000/summary \
  -H "Content-Type: application/json" \
  -d '{
    "data": {
      "pet": "Nala (cat)",
      "reason": "vomiting for 2 days",
      "exam": "mild dehydration",
      "treatment": "subcutaneous fluids + antiemetic injection",
      "plan": "bland diet, monitor closely, recheck in 48h if not improving"
    }
  }'
```

### Response

```json
{
  "summary": "Nala (cat) was seen for vomiting for 2 days and found to have mild dehydration, treated with subcutaneous fluids and an antiemetic injection, and the plan is bland diet, monitor closely, and recheck in 48h if not improving.",
  "model": "phi3:mini"
}
```

---

## 📜 API Contract

### `POST /summary`

**Request**

```json
{
  "data": string | object
}
```

* `data` may be:

  * Free text
  * Structured object (recommended)

**Response**

```json
{
  "summary": string,
  "model": string
}
```

### `GET /health`

Returns service status and active model.

---

## 🔒 Deterministic Behavior (Important)

This service guarantees:

* All provided visit fields **will appear** in the output
* No field is summarized away or omitted
* Output is always **exactly one sentence**

This is achieved by:

* Placeholder-based prompting
* Server-side value injection
* Deterministic fallback templates
* Optional verification & retry logic

---

## ⚙️ Configuration

Environment variables (via `docker-compose.yml`):

| Variable       | Description      | Default     |
| -------------- | ---------------- | ----------- |
| `OLLAMA_MODEL` | Local model name | `phi3:mini` |
| `TEMPERATURE`  | LLM temperature  | `0.2`       |
| `NUM_PREDICT`  | Max tokens       | `120`       |
| `PORT`         | API port         | `3000`      |

---

## 🔁 Switching Models

To switch models (still CPU-only):

```bash
ollama pull qwen2.5:3b
```

Then update:

```yaml
OLLAMA_MODEL: qwen2.5:3b
```

Recommended alternatives:

* `phi3:mini` (fastest, smallest)
* `qwen2.5:3b` (best phrasing)
* `llama3.2:3b` (general purpose)

---

## 🧪 Testing Checklist

* [ ] `GET /health` returns `ok: true`
* [ ] Summary always includes **pet, reason, exam, treatment, plan**
* [ ] Output is exactly **one sentence**
* [ ] No external network calls during runtime

---

## 🧩 Intended Use Cases

* Veterinary visit summaries for pet owners
* SMS / WhatsApp follow-ups
* Discharge notes
* Client portals
* Internal CRM notes (friendly format)


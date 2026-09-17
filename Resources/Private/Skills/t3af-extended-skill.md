# T3AF Extended Assistant

Sample MCP skill for EXT:ns_t3af_extended — the AI Foundation integrator reference.

## Trigger

`/t3af-extended`

## Tools

| Tool | Type | Purpose |
|---|---|---|
| `t3af_extended_echo` | Non-AI | Greeting echo; reads `greetingPrefix` from AI Features settings |
| `t3af_extended_summarize` | AI | Summarizes text via `AiServiceInterface` (JSON only; no persist) |
| `t3af_extended_summarize_content` | AI + persist | Summarizes into an existing `tt_content` uid and binds AI Label |

## Prerequisites

1. Activate **ns_t3af** and **ns_t3af_extended**.
2. Configure a real provider under **AI Foundation → Providers** (the stub adapter only simulates responses).
3. Enable **Enable demo AI features** under **AI Foundation → AI Features → T3AF Extended**.
4. Flush caches after DI or settings changes.

## Example prompts

- Echo: “Say hello to Alice using t3af_extended_echo.”
- Summarize: “Summarize this paragraph with t3af_extended_summarize in concise tone: …”
- Summarize into content: “Use t3af_extended_summarize_content on tt_content uid 12.”
- Then open **AI Foundation → AI Label** (Texts) — recording source `ns_t3af_extended`.

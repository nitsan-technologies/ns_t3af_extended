# T3AF Extended Assistant

Sample MCP skill for EXT:ns_t3af_extended — the AI Foundation integrator reference.

## Trigger

`/t3af-extended`

## Tools

| Tool | Type | Purpose |
|---|---|---|
| `t3af_extended_echo` | Non-AI | Greeting echo; reads `greetingPrefix` from AI Features settings |
| `t3af_extended_summarize` | AI | Summarizes text via `AiServiceInterface` and the `extended_summary` prompt |

## Prerequisites

1. Activate **ns_t3af** and **ns_t3af_extended**.
2. Configure a real provider under **AI Foundation → Providers** (the stub adapter only simulates responses).
3. Enable **Enable demo AI features** under **AI Foundation → AI Features → T3AF Extended**.
4. Flush caches after DI or settings changes.

## Example prompts

- Echo: “Say hello to Alice using t3af_extended_echo.”
- Summarize: “Summarize this paragraph with t3af_extended_summarize in concise tone: …”

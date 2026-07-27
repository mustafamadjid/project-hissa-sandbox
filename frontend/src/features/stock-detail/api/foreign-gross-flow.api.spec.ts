import { beforeEach, describe, expect, it, vi } from "vitest";
vi.mock("@/shared/api/http-client", () => ({ httpClient: { get: vi.fn() }, toApiError: (error: unknown) => error, withRequestSignal: () => ({}) }));
import { httpClient } from "@/shared/api/http-client";
import { fetchForeignGrossFlow } from "./foreign-gross-flow.api";
describe("fetchForeignGrossFlow", () => { beforeEach(() => vi.mocked(httpClient.get).mockReset()); it("serializes path and query", async () => { vi.mocked(httpClient.get).mockResolvedValue({ data: { stock_code: "BBRI", points: [], meta: { unit: "IDR", granularity: "daily" } } }); await fetchForeignGrossFlow({ stock_code: "BBRI", start_date: "2026-07-01", end_date: "2026-07-10" }); expect(httpClient.get).toHaveBeenCalledWith("/market/stocks/BBRI/foreign/gross-flow", expect.objectContaining({ params: { start_date: "2026-07-01", end_date: "2026-07-10", granularity: "daily" } })); }); });

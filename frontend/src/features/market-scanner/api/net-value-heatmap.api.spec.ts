import { beforeEach, describe, expect, it, vi } from "vitest";
vi.mock("@/shared/api/http-client", () => ({ httpClient: { get: vi.fn() }, toApiError: (error: unknown) => error, withRequestSignal: () => ({}) }));
import { httpClient } from "@/shared/api/http-client";
import { fetchNetValueHeatmap } from "./net-value-heatmap.api";
describe("fetchNetValueHeatmap", () => { beforeEach(() => vi.mocked(httpClient.get).mockReset()); it("serializes date query", async () => { vi.mocked(httpClient.get).mockResolvedValue({ data: { dates: [], stocks: [], cells: [], meta: { color_min: -1, color_max: 1 } } }); await fetchNetValueHeatmap({ start_date: "2026-07-01", end_date: "2026-07-10" }); expect(httpClient.get).toHaveBeenCalledWith("/market/net-value-heatmap", expect.objectContaining({ params: { start_date: "2026-07-01", end_date: "2026-07-10" } })); }); });

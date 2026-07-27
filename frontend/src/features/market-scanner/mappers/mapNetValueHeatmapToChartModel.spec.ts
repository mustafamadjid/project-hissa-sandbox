import { describe, expect, it } from "vitest";
import { mapNetValueHeatmapToChartModel } from "./mapNetValueHeatmapToChartModel";
describe("mapNetValueHeatmapToChartModel", () => { it("maps coordinates and keeps missing cells absent", () => { const model = mapNetValueHeatmapToChartModel({ dates: ["2026-07-01", "2026-07-02"], stocks: ["BBCA", "BBRI"], cells: [{ date: "2026-07-02", stock_code: "BBRI", net_value: 10, normalized_value: 0.5 }], meta: { color_min: -10, color_max: 10 } }); expect(model.data).toEqual([[1, 1, 10, 0.5]]); expect(model.data).toHaveLength(1); }); });

import { describe, expect, it } from "vitest";
import {
  mapNetValueTrendToChartModel,
  mapNetValueTrendToOption,
} from "./mapNetValueTrendToChartModel";
import type { NetValueTrendResponseDto } from "../types/net-value-trend.types";

const sample: NetValueTrendResponseDto = {
  stock_code: "BBCA",
  period: { start_date: "2026-07-01", end_date: "2026-07-03" },
  points: [
    {
      date: "2026-07-01",
      stock_code: "BBCA",
      net_value: 1250000000,
      classification: "accumulation",
    },
    {
      date: "2026-07-02",
      stock_code: "BBCA",
      net_value: -500000000,
      classification: "distribution",
    },
  ],
  meta: { unit: "IDR" },
};

describe("mapNetValueTrendToChartModel", () => {
  it("maps points to labels and values", () => {
    const model = mapNetValueTrendToChartModel(sample);
    expect(model.labels).toEqual(["2026-07-01", "2026-07-02"]);
    expect(model.values).toEqual([1250000000, -500000000]);
    expect(model.classifications[0]).toBe("accumulation");
  });

  it("includes zero mark line in option", () => {
    const model = mapNetValueTrendToChartModel(sample);
    const option = mapNetValueTrendToOption(model);
    const series = option.series as Array<{ markLine?: { data?: unknown[] } }>;
    expect(series[0]?.markLine?.data).toEqual([{ yAxis: 0 }]);
  });
});

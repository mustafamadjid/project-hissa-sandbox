import { describe, expect, it } from "vitest";
import {
  isDominanceRatioOutOfTolerance,
  mapDominanceRatioToChartModel,
  mapDominanceRatioToOption,
} from "./mapDominanceRatioToChartModel";
import type { DominanceRatioResponseDto } from "../types/dominance-ratio.types";

describe("isDominanceRatioOutOfTolerance", () => {
  it("accepts values within 99.5-100.5", () => {
    expect(isDominanceRatioOutOfTolerance(100)).toBe(false);
    expect(isDominanceRatioOutOfTolerance(99.5)).toBe(false);
    expect(isDominanceRatioOutOfTolerance(100.5)).toBe(false);
  });

  it("flags values outside tolerance", () => {
    expect(isDominanceRatioOutOfTolerance(99.4)).toBe(true);
    expect(isDominanceRatioOutOfTolerance(100.6)).toBe(true);
  });
});

describe("mapDominanceRatioToChartModel", () => {
  const sample: DominanceRatioResponseDto = {
    stock_code: "BBCA",
    period: { start_date: "2026-07-01", end_date: "2026-07-02" },
    granularity: "daily",
    points: [
      {
        date: "2026-07-01",
        institution_ratio: 52.5,
        retail_ratio: 35,
        mixed_ratio: 12.5,
        total_ratio: 100,
      },
      {
        date: "2026-07-02",
        institution_ratio: 40,
        retail_ratio: 40,
        mixed_ratio: 10,
        total_ratio: 90,
      },
    ],
    meta: {
      ratio_basis: "transaction_value",
      unit: "percent",
      aggregation: "daily",
      timezone: "Asia/Jakarta",
    },
  };

  it("maps segments and quality warning without renormalizing", () => {
    const model = mapDominanceRatioToChartModel(sample);
    expect(model.segments).toHaveLength(2);
    expect(model.segments[1]?.totalRatio).toBe(90);
    expect(model.segments[1]?.isTotalOutOfTolerance).toBe(true);
    expect(model.hasQualityWarning).toBe(true);
  });

  it("builds stacked bar option with three series", () => {
    const model = mapDominanceRatioToChartModel(sample);
    const option = mapDominanceRatioToOption(model);
    const series = option.series as unknown[];
    expect(series).toHaveLength(3);
    expect(option.xAxis).toMatchObject({ type: "category" });
    expect(option.yAxis).toMatchObject({ type: "value", min: 0, max: 100 });
  });
});

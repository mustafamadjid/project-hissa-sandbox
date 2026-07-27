import { describe, expect, it } from "vitest";
import {
  mapNetValueRankingToChartModel,
  mapNetValueRankingToOption,
} from "./mapNetValueRankingToChartModel";
import type { NetValueRankingResponseDto } from "../types/net-value-ranking.types";

const sample: NetValueRankingResponseDto = {
  period: { start_date: "2026-07-01", end_date: "2026-07-22" },
  items: [
    {
      rank: 1,
      stock_code: "GOTO",
      net_value: -3200000000,
      classification: "distribution",
    },
    {
      rank: 1,
      stock_code: "BBCA",
      net_value: 2500000000,
      classification: "accumulation",
    },
  ],
  meta: { limit: 10, aggregation: "sum", unit: "IDR" },
};

describe("mapNetValueRankingToChartModel", () => {
  it("maps DTO to chart model preserving order", () => {
    const model = mapNetValueRankingToChartModel(sample);
    expect(model.bars).toHaveLength(2);
    expect(model.bars[0]?.stockCode).toBe("GOTO");
    expect(model.bars[1]?.stockCode).toBe("BBCA");
    expect(model.bars[0]?.label).toContain("GOTO");
  });

  it("builds echarts option with zero mark line", () => {
    const model = mapNetValueRankingToChartModel(sample);
    const option = mapNetValueRankingToOption(model);
    const series = option.series as Array<{ markLine?: { data?: unknown[] } }>;
    expect(series[0]?.markLine?.data).toEqual([{ xAxis: 0 }]);
  });
});

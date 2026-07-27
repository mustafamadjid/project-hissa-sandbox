import { describe, expect, it } from "vitest";
import {
  getQuadrantLabel,
  mapForeignFlowScatterToChartModel,
  mapForeignFlowScatterToOption,
} from "./mapForeignFlowScatterToChartModel";
import type { ForeignFlowScatterResponseDto } from "@/features/cross-sectional-analysis/types/foreign-flow-scatter.types";

const sample: ForeignFlowScatterResponseDto = {
  period: { start_date: "2026-07-01", end_date: "2026-07-22" },
  items: [
    {
      stock_code: "BBRI",
      foreign_net_flow: 2_000_000_000,
      net_value: 2_500_000_000,
      domestic_net_flow: 500_000_000,
      quadrant: "foreign_buy_accumulation",
    },
    {
      stock_code: "TLKM",
      foreign_net_flow: -1_000_000_000,
      net_value: -2_000_000_000,
      domestic_net_flow: 100_000_000,
      quadrant: "foreign_sell_distribution",
    },
  ],
  meta: { unit: "IDR", aggregation: "sum" },
};

describe("mapForeignFlowScatterToChartModel", () => {
  it("maps DTO to chart model without recalculating quadrant", () => {
    const model = mapForeignFlowScatterToChartModel(sample);
    expect(model.points).toEqual([
      {
        stockCode: "BBRI",
        foreignNetFlow: 2_000_000_000,
        netValue: 2_500_000_000,
        domesticNetFlow: 500_000_000,
        quadrant: "foreign_buy_accumulation",
      },
      {
        stockCode: "TLKM",
        foreignNetFlow: -1_000_000_000,
        netValue: -2_000_000_000,
        domesticNetFlow: 100_000_000,
        quadrant: "foreign_sell_distribution",
      },
    ]);
    expect(model.aggregation).toBe("sum");
  });

  it("maps quadrant labels", () => {
    expect(getQuadrantLabel("foreign_buy_accumulation")).toBe("Beli asing · Akumulasi");
    expect(getQuadrantLabel("foreign_sell_distribution")).toBe("Jual asing · Distribusi");
  });

  it("builds scatter option with zero mark lines and tooltip fields", () => {
    const option = mapForeignFlowScatterToOption(mapForeignFlowScatterToChartModel(sample));
    const seriesList = (option.series ?? []) as unknown as Array<{
      type?: string;
      data?: unknown[];
      markLine?: { data?: unknown[] };
    }>;
    const series = seriesList[0];
    expect(series).toBeDefined();
    if (!series) return;
    expect(series.type).toBe("scatter");
    expect(series.data).toHaveLength(2);
    expect(series.markLine?.data).toEqual([{ xAxis: 0 }, { yAxis: 0 }]);

    const formatter = (option.tooltip as { formatter?: (params: unknown) => string }).formatter;
    expect(formatter).toBeTypeOf("function");
    const html = formatter?.({ dataIndex: 0 }) ?? "";
    expect(html).toContain("BBRI");
    expect(html).toContain("Foreign net flow");
    expect(html).toContain("Net value");
    expect(html).toContain("Domestic net flow");
    expect(html).toContain("Kuadran");
  });
});

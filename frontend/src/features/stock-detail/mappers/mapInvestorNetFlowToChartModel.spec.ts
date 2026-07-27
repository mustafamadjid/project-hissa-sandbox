import { describe, expect, it } from "vitest";
import {
  mapInvestorNetFlowToChartModel,
  mapInvestorNetFlowToOption,
} from "./mapInvestorNetFlowToChartModel";
import type { InvestorNetFlowResponseDto } from "../types/investor-net-flow.types";

const sample: InvestorNetFlowResponseDto = {
  stock_code: "BBCA",
  points: [
    {
      date: "2026-07-01",
      foreign_net_flow: 900000000,
      domestic_net_flow: -900000000,
    },
  ],
  meta: { unit: "IDR", granularity: "daily" },
};

describe("mapInvestorNetFlowToChartModel", () => {
  it("maps foreign and domestic series separately", () => {
    const model = mapInvestorNetFlowToChartModel(sample);
    expect(model.foreignValues).toEqual([900000000]);
    expect(model.domesticValues).toEqual([-900000000]);
  });

  it("builds multi-line option with two series", () => {
    const model = mapInvestorNetFlowToChartModel(sample);
    const option = mapInvestorNetFlowToOption(model);
    const series = option.series as unknown[];
    expect(series).toHaveLength(2);
  });
});

import type { DateRangeParams } from "@/shared/types/api.types";

export interface InvestorNetFlowParams extends DateRangeParams {
  stock_code: string;
  granularity?: "daily";
}

export interface InvestorNetFlowPointDto {
  date: string;
  foreign_net_flow: number;
  domestic_net_flow: number;
}

export interface InvestorNetFlowResponseDto {
  stock_code: string;
  points: InvestorNetFlowPointDto[];
  meta: {
    unit: "IDR";
    granularity: "daily";
  };
}

export interface InvestorNetFlowChartModel {
  stockCode: string;
  labels: string[];
  foreignValues: number[];
  domesticValues: number[];
  unit: "IDR";
  granularity: "daily";
}

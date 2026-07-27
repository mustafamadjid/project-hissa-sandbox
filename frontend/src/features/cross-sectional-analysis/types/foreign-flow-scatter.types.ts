import type { DatePeriod, DateRangeParams } from "@/shared/types/api.types";

export type ForeignFlowQuadrant =
  | "foreign_buy_accumulation"
  | "foreign_sell_distribution"
  | "foreign_buy_distribution"
  | "foreign_sell_accumulation";

export interface ForeignFlowScatterParams extends DateRangeParams {
  aggregation?: "sum";
  stock_codes?: string;
  min_abs_value?: number;
  limit?: number;
}

export interface ForeignFlowScatterItemDto {
  stock_code: string;
  foreign_net_flow: number;
  net_value: number;
  domestic_net_flow: number;
  quadrant: ForeignFlowQuadrant;
}

export interface ForeignFlowScatterResponseDto {
  period: DatePeriod;
  items: ForeignFlowScatterItemDto[];
  meta: { unit: "IDR"; aggregation: "sum" };
}

export interface ForeignFlowScatterPointModel {
  stockCode: string;
  foreignNetFlow: number;
  netValue: number;
  domesticNetFlow: number;
  quadrant: ForeignFlowQuadrant;
}

export interface ForeignFlowScatterChartModel {
  points: ForeignFlowScatterPointModel[];
  period: DatePeriod;
  unit: "IDR";
  aggregation: "sum";
}

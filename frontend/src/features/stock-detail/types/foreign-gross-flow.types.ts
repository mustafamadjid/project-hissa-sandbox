import type { DateRangeParams } from "@/shared/types/api.types";

export interface ForeignGrossFlowParams extends DateRangeParams { stock_code: string; granularity?: "daily" }
export interface ForeignGrossFlowResponseDto {
  stock_code: string;
  points: Array<{ date: string; foreign_buy: number; foreign_sell: number; foreign_net_flow: number }>;
  meta: { unit: "IDR"; granularity: "daily" };
}
export interface ForeignGrossFlowChartModel {
  stockCode: string; labels: string[]; buyValues: number[]; sellValues: number[]; netValues: number[];
}

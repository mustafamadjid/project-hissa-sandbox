import type { DateRangeParams } from "@/shared/types/api.types";

export interface CumulativeNetValueParams extends DateRangeParams { stock_code: string; reset?: "start_of_period" }
export interface CumulativeNetValueResponseDto {
  stock_code: string;
  period: DateRangeParams;
  points: Array<{ date: string; daily_net_value: number; cumulative_net_value: number }>;
  meta: { reset_policy: "start_of_period"; unit: "IDR"; granularity: "daily" };
}
export interface CumulativeNetValueChartModel {
  stockCode: string; period: DateRangeParams; labels: string[]; dailyValues: number[]; cumulativeValues: number[]; resetPolicy: "start_of_period";
}

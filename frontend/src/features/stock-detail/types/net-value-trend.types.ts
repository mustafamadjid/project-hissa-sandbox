import type { DatePeriod, DateRangeParams } from "@/shared/types/api.types";

export interface NetValueTrendParams extends DateRangeParams {
  stock_code: string;
}

export interface NetValueTrendPointDto {
  date: string;
  stock_code: string;
  net_value: number;
  classification: string;
}

export interface NetValueTrendResponseDto {
  stock_code: string;
  period: DatePeriod;
  points: NetValueTrendPointDto[];
  meta: {
    unit: "IDR";
    updated_at?: string;
  };
}

export interface NetValueTrendChartModel {
  stockCode: string;
  period: DatePeriod;
  labels: string[];
  values: number[];
  classifications: string[];
  unit: "IDR";
  updatedAt?: string;
}

import type { DateRangeParams } from "@/shared/types/api.types";

export interface DominanceRatioParams extends DateRangeParams {
  stock_code: string;
  granularity: "daily" | "weekly" | "monthly";
}

export interface DominanceRatioPointDto {
  date: string;
  institution_ratio: number;
  retail_ratio: number;
  mixed_ratio: number;
  total_ratio: number;
}

export interface DominanceRatioResponseDto {
  stock_code: string;
  period: DateRangeParams;
  granularity: DominanceRatioParams["granularity"];
  points: DominanceRatioPointDto[];
  meta: {
    ratio_basis: "transaction_value";
    unit: "percent";
    aggregation: "daily" | "latest";
    timezone: string;
  };
}

export interface DominanceRatioSegmentModel {
  date: string;
  institution: number;
  retail: number;
  mixed: number;
  totalRatio: number;
  isTotalOutOfTolerance: boolean;
  label: string;
}

export interface DominanceRatioChartModel {
  stockCode: string;
  segments: DominanceRatioSegmentModel[];
  ratioBasis: "transaction_value";
  unit: "percent";
  hasQualityWarning: boolean;
}

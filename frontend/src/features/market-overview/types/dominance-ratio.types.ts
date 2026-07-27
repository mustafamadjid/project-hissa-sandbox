import type { DateRangeParams } from "@/shared/types/api.types";

export interface DominanceRatioParams extends DateRangeParams {
  stock_code?: string;
}

export interface DominanceRatioItemDto {
  date: string;
  stock_code: string;
  institution: number;
  retail: number;
  mixed: number;
  total_ratio: number;
}

export interface DominanceRatioResponseDto {
  items: DominanceRatioItemDto[];
  meta: {
    ratio_basis: "transaction_value";
    unit: "percent";
  };
}

export interface DominanceRatioSegmentModel {
  date: string;
  stockCode: string;
  institution: number;
  retail: number;
  mixed: number;
  totalRatio: number;
  isTotalOutOfTolerance: boolean;
  label: string;
}

export interface DominanceRatioChartModel {
  segments: DominanceRatioSegmentModel[];
  ratioBasis: "transaction_value";
  unit: "percent";
  hasQualityWarning: boolean;
}

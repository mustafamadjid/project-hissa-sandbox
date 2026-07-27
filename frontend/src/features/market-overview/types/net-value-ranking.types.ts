import type { DatePeriod, DateRangeParams } from "@/shared/types/api.types";

export type RankingClassification = "accumulation" | "distribution";

export interface NetValueRankingParams extends DateRangeParams {
  limit?: number;
}

export interface NetValueRankingItemDto {
  rank: number;
  stock_code: string;
  net_value: number;
  classification: RankingClassification;
}

export interface NetValueRankingResponseDto {
  period: DatePeriod;
  items: NetValueRankingItemDto[];
  meta: {
    limit: number;
    aggregation: "sum";
    unit: "IDR";
  };
}

export interface NetValueRankingBarModel {
  stockCode: string;
  rank: number;
  netValue: number;
  classification: RankingClassification;
  label: string;
}

export interface NetValueRankingChartModel {
  bars: NetValueRankingBarModel[];
  period: DatePeriod;
  unit: "IDR";
  limit: number;
}

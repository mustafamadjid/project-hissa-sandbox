import type { DateRangeParams } from "@/shared/types/api.types";

export type NetValueHeatmapParams = DateRangeParams;

export interface NetValueHeatmapCellDto {
  date: string;
  stock_code: string;
  net_value: number;
  normalized_value: number;
}

export interface NetValueHeatmapResponseDto {
  dates: string[];
  stocks: string[];
  cells: NetValueHeatmapCellDto[];
  meta: { color_min: number; color_max: number };
}

export interface NetValueHeatmapChartModel extends NetValueHeatmapResponseDto {
  data: Array<[number, number, number, number]>;
}

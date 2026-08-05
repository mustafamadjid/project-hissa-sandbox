import type {
  DominanceRatioChartModel,
  DominanceRatioResponseDto,
  DominanceRatioSegmentModel,
} from "@/features/market-overview/types/dominance-ratio.types";
import type { ChartOption } from "@/shared/components/charts/BaseChart.vue";
import {
  CHART_PALETTE,
  DOMINANCE_RATIO_TOLERANCE,
} from "@/shared/constants/chart-palette";
import { formatApiDateShort } from "@/shared/formatters/date";
import { formatPercentRatio } from "@/shared/formatters/percentage";

const BAR_MAX_WIDTH = 44;

export function isDominanceRatioOutOfTolerance(totalRatio: number): boolean {
  return (
    totalRatio < DOMINANCE_RATIO_TOLERANCE.min ||
    totalRatio > DOMINANCE_RATIO_TOLERANCE.max
  );
}

export function mapDominanceRatioToChartModel(
  dto: DominanceRatioResponseDto,
): DominanceRatioChartModel {
  const segments: DominanceRatioSegmentModel[] = dto.points.map((item) => {
    const outOfTolerance = isDominanceRatioOutOfTolerance(item.total_ratio);
    return {
      date: item.date,
      institution: item.institution_ratio,
      retail: item.retail_ratio,
      mixed: item.mixed_ratio,
      totalRatio: item.total_ratio,
      isTotalOutOfTolerance: outOfTolerance,
      label: formatApiDateShort(item.date),
    };
  });

  return {
    stockCode: dto.stock_code,
    segments,
    ratioBasis: dto.meta.ratio_basis,
    unit: dto.meta.unit,
    hasQualityWarning: segments.some((segment) => segment.isTotalOutOfTolerance),
  };
}

export function mapDominanceRatioToOption(
  model: DominanceRatioChartModel,
): ChartOption {
  const categories = model.segments.map((segment) => segment.label);

  return {
    grid: {
      left: 64,
      right: 28,
      top: 44,
      bottom: 72,
      containLabel: false,
    },
    legend: {
      top: 0,
      data: ["Institusi", "Retail", "Campuran"],
      textStyle: { color: CHART_PALETTE.textMuted },
    },
    tooltip: {
      trigger: "axis",
      axisPointer: { type: "shadow" },
      formatter: (params: unknown) => {
        const items = Array.isArray(params) ? params : [params];
        const first = items[0] as { dataIndex?: number };
        const index = first.dataIndex ?? 0;
        const segment = model.segments[index];
        if (!segment) return "";

        const lines = [
          `<strong>${model.stockCode}</strong> · ${segment.label}`,
          `Institusi: ${formatPercentRatio(segment.institution)}`,
          `Retail: ${formatPercentRatio(segment.retail)}`,
          `Campuran: ${formatPercentRatio(segment.mixed)}`,
          `Total: ${formatPercentRatio(segment.totalRatio)}`,
          `Basis: nilai transaksi`,
        ];
        if (segment.isTotalOutOfTolerance) {
          lines.push("Peringatan: total di luar 99.5–100.5%");
        }
        return lines.join("<br/>");
      },
    },
    xAxis: {
      type: "category",
      data: categories,
      axisLabel: {
        color: CHART_PALETTE.axis,
        rotate: categories.length > 15 ? 45 : 0,
      },
      splitLine: {
        lineStyle: { color: CHART_PALETTE.grid },
      },
    },
    yAxis: {
      type: "value",
      min: 0,
      max: 100,
      name: "Persentase",
      axisLabel: {
        color: CHART_PALETTE.axis,
        formatter: (value: number) => `${value}%`,
      },
    },
    series: [
      {
        name: "Institusi",
        type: "bar",
        stack: "ratio",
        data: model.segments.map((segment) => segment.institution),
        itemStyle: { color: CHART_PALETTE.institution },
        barMaxWidth: BAR_MAX_WIDTH,
        barCategoryGap: "28%",
        label: { show: false },
      },
      {
        name: "Retail",
        type: "bar",
        stack: "ratio",
        data: model.segments.map((segment) => segment.retail),
        itemStyle: { color: CHART_PALETTE.retail },
        barMaxWidth: BAR_MAX_WIDTH,
        label: { show: false },
      },
      {
        name: "Campuran",
        type: "bar",
        stack: "ratio",
        data: model.segments.map((segment) => segment.mixed),
        itemStyle: { color: CHART_PALETTE.mixed },
        barMaxWidth: BAR_MAX_WIDTH,
        label: { show: false },
      },
    ],
    dataZoom: [
      { type: "inside", start: 0, end: 100 },
      { type: "slider", start: 0, end: 100 },
    ],
  };
}

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
  const segments: DominanceRatioSegmentModel[] = dto.items.map((item) => {
    const outOfTolerance = isDominanceRatioOutOfTolerance(item.total_ratio);
    return {
      date: item.date,
      stockCode: item.stock_code,
      institution: item.institution,
      retail: item.retail,
      mixed: item.mixed,
      totalRatio: item.total_ratio,
      isTotalOutOfTolerance: outOfTolerance,
      label: `${item.stock_code} · ${formatApiDateShort(item.date)}`,
    };
  });

  return {
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
      left: 132,
      right: 28,
      top: 44,
      bottom: 28,
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
          `<strong>${segment.stockCode}</strong> · ${segment.date}`,
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
      type: "value",
      max: 100,
      axisLabel: {
        color: CHART_PALETTE.axis,
        formatter: (value: number) => `${value}%`,
      },
      splitLine: {
        lineStyle: { color: CHART_PALETTE.grid },
      },
    },
    yAxis: {
      type: "category",
      data: categories,
      inverse: true,
      axisLabel: {
        color: CHART_PALETTE.text,
        width: 120,
        overflow: "truncate",
      },
      axisTick: { show: false },
      axisLine: { show: false },
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
  };
}

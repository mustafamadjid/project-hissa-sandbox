import type {
  NetValueTrendChartModel,
  NetValueTrendResponseDto,
} from "@/features/stock-detail/types/net-value-trend.types";
import type { ChartOption } from "@/shared/components/charts/BaseChart.vue";
import { CHART_PALETTE } from "@/shared/constants/chart-palette";
import { formatIdr } from "@/shared/formatters/currency";
import { formatApiDate, formatApiDateShort } from "@/shared/formatters/date";
import { formatAxisIdr } from "@/shared/formatters/number";

const DATA_ZOOM_THRESHOLD = 40;

function classificationLabel(value: string): string {
  const lower = value.toLowerCase();
  if (lower === "accumulation" || lower === "akumulasi") return "Akumulasi";
  if (lower === "distribution" || lower === "distribusi") return "Distribusi";
  return value;
}

export function mapNetValueTrendToChartModel(
  dto: NetValueTrendResponseDto,
): NetValueTrendChartModel {
  return {
    stockCode: dto.stock_code,
    period: dto.period,
    labels: dto.points.map((point) => point.date),
    values: dto.points.map((point) => point.net_value),
    classifications: dto.points.map((point) => point.classification),
    unit: dto.meta.unit,
    ...(dto.meta.updated_at !== undefined
      ? { updatedAt: dto.meta.updated_at }
      : {}),
  };
}

export function mapNetValueTrendToOption(
  model: NetValueTrendChartModel,
): ChartOption {
  const showDataZoom = model.labels.length > DATA_ZOOM_THRESHOLD;

  const option: ChartOption = {
    grid: {
      left: 56,
      right: 24,
      top: 24,
      bottom: showDataZoom ? 72 : 40,
      containLabel: true,
    },
    tooltip: {
      trigger: "axis",
      axisPointer: { type: "cross" },
      formatter: (params: unknown) => {
        const items = Array.isArray(params) ? params : [params];
        const first = items[0] as { dataIndex?: number; value?: number };
        const index = first.dataIndex ?? 0;
        const date = model.labels[index];
        const value = model.values[index];
        const classification = model.classifications[index];
        if (date === undefined || value === undefined) return "";

        const lines = [
          `<strong>${formatApiDate(date)}</strong>`,
          `Net value: ${formatIdr(value)}`,
          value > 0
            ? "Arah: positif (+)"
            : value < 0
              ? "Arah: negatif (−)"
              : "Arah: nol",
        ];
        if (classification) {
          lines.push(`Klasifikasi: ${classificationLabel(classification)}`);
        }
        return lines.join("<br/>");
      },
    },
    xAxis: {
      type: "category",
      data: model.labels,
      boundaryGap: false,
      axisLabel: {
        color: CHART_PALETTE.axis,
        formatter: (value: string) => formatApiDateShort(value),
        hideOverlap: true,
      },
      axisLine: { lineStyle: { color: CHART_PALETTE.grid } },
    },
    yAxis: {
      type: "value",
      axisLabel: {
        color: CHART_PALETTE.axis,
        formatter: (value: number) => formatAxisIdr(value),
      },
      splitLine: {
        lineStyle: { color: CHART_PALETTE.grid },
      },
    },
    series: [
      {
        name: "Net value",
        type: "line",
        data: model.values.map((value) => ({
          value,
          itemStyle: {
            color:
              value > 0
                ? CHART_PALETTE.accumulation
                : value < 0
                  ? CHART_PALETTE.distribution
                  : CHART_PALETTE.zeroLine,
          },
        })),
        showSymbol: model.values.length <= 60,
        symbolSize: 6,
        lineStyle: {
          width: 2,
          color: CHART_PALETTE.accumulation,
        },
        areaStyle: {
          opacity: 0.08,
          color: CHART_PALETTE.accumulation,
        },
        markLine: {
          symbol: "none",
          label: {
            formatter: "0",
            color: CHART_PALETTE.textMuted,
            fontSize: 10,
          },
          data: [{ yAxis: 0 }],
          lineStyle: {
            color: CHART_PALETTE.zeroLine,
            type: "dashed",
            width: 1,
          },
        },
      },
    ],
  };

  if (showDataZoom) {
    option.dataZoom = [
      { type: "inside", start: 0, end: 100 },
      { type: "slider", start: 0, end: 100, height: 18, bottom: 8 },
    ];
  }

  return option;
}

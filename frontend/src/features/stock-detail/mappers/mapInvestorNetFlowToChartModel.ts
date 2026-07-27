import type {
  InvestorNetFlowChartModel,
  InvestorNetFlowResponseDto,
} from "@/features/stock-detail/types/investor-net-flow.types";
import type { ChartOption } from "@/shared/components/charts/BaseChart.vue";
import { CHART_PALETTE } from "@/shared/constants/chart-palette";
import { formatIdr } from "@/shared/formatters/currency";
import { formatApiDate, formatApiDateShort } from "@/shared/formatters/date";
import { formatAxisIdr } from "@/shared/formatters/number";

const DATA_ZOOM_THRESHOLD = 40;

export function mapInvestorNetFlowToChartModel(
  dto: InvestorNetFlowResponseDto,
): InvestorNetFlowChartModel {
  return {
    stockCode: dto.stock_code,
    labels: dto.points.map((point) => point.date),
    foreignValues: dto.points.map((point) => point.foreign_net_flow),
    domesticValues: dto.points.map((point) => point.domestic_net_flow),
    unit: dto.meta.unit,
    granularity: dto.meta.granularity,
  };
}

export function mapInvestorNetFlowToOption(
  model: InvestorNetFlowChartModel,
): ChartOption {
  const showDataZoom = model.labels.length > DATA_ZOOM_THRESHOLD;

  const option: ChartOption = {
    grid: {
      left: 56,
      right: 24,
      top: 40,
      bottom: showDataZoom ? 72 : 40,
      containLabel: true,
    },
    legend: {
      top: 0,
      data: ["Foreign net flow", "Domestic net flow"],
      textStyle: { color: CHART_PALETTE.textMuted },
      selectedMode: true,
    },
    tooltip: {
      trigger: "axis",
      axisPointer: { type: "cross" },
      formatter: (params: unknown) => {
        const items = Array.isArray(params) ? params : [params];
        const first = items[0] as { dataIndex?: number };
        const index = first.dataIndex ?? 0;
        const date = model.labels[index];
        if (!date) return "";

        const lines = [`<strong>${formatApiDate(date)}</strong>`];
        for (const item of items as Array<{
          seriesName?: string;
          value?: number;
          marker?: string;
        }>) {
          const value = item.value ?? 0;
          lines.push(
            `${item.marker ?? ""}${item.seriesName}: ${formatIdr(value)}`,
          );
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
        name: "Foreign net flow",
        type: "line",
        data: model.foreignValues,
        showSymbol: model.foreignValues.length <= 60,
        symbolSize: 6,
        lineStyle: { width: 2, color: CHART_PALETTE.foreign },
        itemStyle: { color: CHART_PALETTE.foreign },
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
      {
        name: "Domestic net flow",
        type: "line",
        data: model.domesticValues,
        showSymbol: model.domesticValues.length <= 60,
        symbolSize: 6,
        lineStyle: { width: 2, color: CHART_PALETTE.domestic },
        itemStyle: { color: CHART_PALETTE.domestic },
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

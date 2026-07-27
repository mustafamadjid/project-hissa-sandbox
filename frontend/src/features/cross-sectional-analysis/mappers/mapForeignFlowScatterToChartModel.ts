import type { ChartOption } from "@/shared/components/charts/BaseChart.vue";
import { CHART_PALETTE } from "@/shared/constants/chart-palette";
import { formatIdr } from "@/shared/formatters/currency";
import { formatAxisIdr } from "@/shared/formatters/number";
import type { ForeignFlowQuadrant, ForeignFlowScatterChartModel, ForeignFlowScatterResponseDto } from "@/features/cross-sectional-analysis/types/foreign-flow-scatter.types";

const quadrantLabels: Record<ForeignFlowQuadrant, string> = {
  foreign_buy_accumulation: "Beli asing · Akumulasi",
  foreign_sell_distribution: "Jual asing · Distribusi",
  foreign_buy_distribution: "Beli asing · Distribusi",
  foreign_sell_accumulation: "Jual asing · Akumulasi",
};

const quadrantColors: Record<ForeignFlowQuadrant, string> = {
  foreign_buy_accumulation: CHART_PALETTE.accumulation,
  foreign_sell_distribution: CHART_PALETTE.distribution,
  foreign_buy_distribution: CHART_PALETTE.mixed,
  foreign_sell_accumulation: CHART_PALETTE.domestic,
};

export function getQuadrantLabel(quadrant: ForeignFlowQuadrant): string {
  return quadrantLabels[quadrant];
}

export function mapForeignFlowScatterToChartModel(dto: ForeignFlowScatterResponseDto): ForeignFlowScatterChartModel {
  return {
    points: dto.items.map((item) => ({
      stockCode: item.stock_code,
      foreignNetFlow: item.foreign_net_flow,
      netValue: item.net_value,
      domesticNetFlow: item.domestic_net_flow,
      quadrant: item.quadrant,
    })),
    period: dto.period,
    unit: dto.meta.unit,
    aggregation: dto.meta.aggregation,
  };
}

export function mapForeignFlowScatterToOption(model: ForeignFlowScatterChartModel): ChartOption {
  return {
    grid: { left: 68, right: 28, top: 36, bottom: 60, containLabel: true },
    tooltip: {
      trigger: "item",
      formatter: (params: unknown) => {
        const index = (params as { dataIndex?: number }).dataIndex;
        const point = index === undefined ? undefined : model.points[index];
        if (!point) return "";
        return [
          `<strong>${point.stockCode}</strong>`,
          `Foreign net flow: ${formatIdr(point.foreignNetFlow)}`,
          `Net value: ${formatIdr(point.netValue)}`,
          `Domestic net flow: ${formatIdr(point.domesticNetFlow)}`,
          `Kuadran: ${getQuadrantLabel(point.quadrant)}`,
        ].join("<br/>");
      },
    },
    xAxis: {
      type: "value",
      name: "Foreign net flow",
      nameLocation: "middle",
      nameGap: 34,
      axisLabel: { color: CHART_PALETTE.axis, formatter: formatAxisIdr },
      splitLine: { lineStyle: { color: CHART_PALETTE.grid } },
      axisLine: { lineStyle: { color: CHART_PALETTE.zeroLine } },
    },
    yAxis: {
      type: "value",
      name: "Net value",
      nameLocation: "middle",
      nameGap: 48,
      axisLabel: { color: CHART_PALETTE.axis, formatter: formatAxisIdr },
      splitLine: { lineStyle: { color: CHART_PALETTE.grid } },
      axisLine: { lineStyle: { color: CHART_PALETTE.zeroLine } },
    },
    series: [{
      type: "scatter",
      data: model.points.map((point) => ({
        value: [point.foreignNetFlow, point.netValue],
        itemStyle: { color: quadrantColors[point.quadrant] },
      })),
      symbolSize: 10,
      emphasis: { scale: true },
      markLine: {
        symbol: "none",
        label: { show: false },
        data: [{ xAxis: 0 }, { yAxis: 0 }],
        lineStyle: { color: CHART_PALETTE.zeroLine, type: "dashed", width: 1 },
      },
    }],
  };
}

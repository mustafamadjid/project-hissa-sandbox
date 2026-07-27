import type {
  NetValueRankingBarModel,
  NetValueRankingChartModel,
  NetValueRankingResponseDto,
} from "@/features/market-overview/types/net-value-ranking.types";
import type { ChartOption } from "@/shared/components/charts/BaseChart.vue";
import { CHART_PALETTE } from "@/shared/constants/chart-palette";
import { formatIdr } from "@/shared/formatters/currency";
import { formatAxisIdr } from "@/shared/formatters/number";

function classificationLabel(value: string): string {
  return value === "accumulation" ? "Akumulasi" : "Distribusi";
}

/**
 * Backend returns distribution first, then accumulation.
 * Keep backend order for deterministic visual ranking.
 */
export function mapNetValueRankingToChartModel(
  dto: NetValueRankingResponseDto,
): NetValueRankingChartModel {
  const bars: NetValueRankingBarModel[] = dto.items.map((item) => ({
    stockCode: item.stock_code,
    rank: item.rank,
    netValue: item.net_value,
    classification: item.classification,
    label: `${item.stock_code} (#${item.rank})`,
  }));

  return {
    bars,
    period: dto.period,
    unit: dto.meta.unit,
    limit: dto.meta.limit,
  };
}

export function mapNetValueRankingToOption(
  model: NetValueRankingChartModel,
): ChartOption {
  const categories = model.bars.map((bar) => bar.label);
  const values = model.bars.map((bar) => bar.netValue);

  return {
    grid: {
      left: 96,
      right: 72,
      top: 16,
      bottom: 24,
      containLabel: false,
    },
    tooltip: {
      trigger: "axis",
      axisPointer: { type: "shadow" },
      formatter: (params: unknown) => {
        const items = Array.isArray(params) ? params : [params];
        const first = items[0] as {
          dataIndex?: number;
          value?: number;
        };
        const index = first.dataIndex ?? 0;
        const bar = model.bars[index];
        if (!bar) return "";
        return [
          `<strong>${bar.stockCode}</strong>`,
          `Peringkat: ${bar.rank}`,
          `Klasifikasi: ${classificationLabel(bar.classification)}`,
          `Net value: ${formatIdr(bar.netValue)}`,
        ].join("<br/>");
      },
    },
    xAxis: {
      type: "value",
      axisLabel: {
        color: CHART_PALETTE.axis,
        formatter: (value: number) => formatAxisIdr(value),
      },
      splitLine: {
        lineStyle: { color: CHART_PALETTE.grid },
      },
      axisLine: {
        lineStyle: { color: CHART_PALETTE.zeroLine },
      },
    },
    yAxis: {
      type: "category",
      data: categories,
      inverse: true,
      axisLabel: {
        color: CHART_PALETTE.text,
        width: 88,
        overflow: "truncate",
      },
      axisTick: { show: false },
      axisLine: { show: false },
    },
    series: [
      {
        type: "bar",
        data: values.map((value, index) => {
          const bar = model.bars[index];
          const color =
            bar?.classification === "accumulation"
              ? CHART_PALETTE.accumulation
              : CHART_PALETTE.distribution;
          return {
            value,
            itemStyle: { color },
          };
        }),
        barMaxWidth: 36,
        barCategoryGap: "28%",
        label: { show: false },
        markLine: {
          symbol: "none",
          label: { show: false },
          data: [{ xAxis: 0 }],
          lineStyle: {
            color: CHART_PALETTE.zeroLine,
            type: "dashed",
            width: 1,
          },
        },
      },
    ],
  };
}

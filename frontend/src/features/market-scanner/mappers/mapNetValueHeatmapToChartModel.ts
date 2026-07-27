import type { NetValueHeatmapChartModel, NetValueHeatmapResponseDto } from "@/features/market-scanner/types/net-value-heatmap.types";
import type { ChartOption } from "@/shared/components/charts/BaseChart.vue";
import { formatApiDate, formatApiDateShort } from "@/shared/formatters/date";
import { formatIdr } from "@/shared/formatters/currency";

export function mapNetValueHeatmapToChartModel(dto: NetValueHeatmapResponseDto): NetValueHeatmapChartModel {
  const dateIndices = new Map(dto.dates.map((date, index) => [date, index]));
  const stockIndices = new Map(dto.stocks.map((stock, index) => [stock, index]));
  return { ...dto, data: dto.cells.flatMap((cell) => { const x = dateIndices.get(cell.date); const y = stockIndices.get(cell.stock_code); return x === undefined || y === undefined ? [] : [[x, y, cell.net_value, cell.normalized_value]]; }) };
}

export function mapNetValueHeatmapToOption(model: NetValueHeatmapChartModel): ChartOption {
  const broad = model.dates.length > 40 || model.stocks.length > 20;
  const option: ChartOption = { grid: { left: 72, right: 32, top: 24, bottom: broad ? 72 : 40, containLabel: true }, tooltip: { position: "top", formatter: (params: unknown) => { const data = (params as { data?: [number, number, number, number] }).data; if (!data) return ""; return `<strong>${formatApiDate(model.dates[data[0]] ?? "")}</strong><br/>Saham: ${model.stocks[data[1]] ?? ""}<br/>Net value: ${formatIdr(data[2])}<br/>Nilai normalisasi: ${data[3]}`; } }, xAxis: { type: "category", data: model.dates, axisLabel: { formatter: (value: string) => formatApiDateShort(value), hideOverlap: true } }, yAxis: { type: "category", data: model.stocks, axisLabel: { hideOverlap: true } }, visualMap: { min: model.meta.color_min, max: model.meta.color_max, calculable: true, orient: "horizontal", left: "center", bottom: broad ? 28 : 0, inRange: { color: ["#dc2626", "#f3f4f6", "#059669"] } }, series: [{ type: "heatmap", data: model.data }] };
  if (broad) option.dataZoom = [{ type: "inside", xAxisIndex: 0 }, { type: "slider", xAxisIndex: 0, height: 18, bottom: 4 }];
  return option;
}

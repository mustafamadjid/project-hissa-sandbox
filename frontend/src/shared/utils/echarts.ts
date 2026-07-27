import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { BarChart, HeatmapChart, LineChart, ScatterChart } from "echarts/charts";
import {
  DataZoomComponent,
  GridComponent,
  LegendComponent,
  MarkLineComponent,
  TooltipComponent,
  VisualMapComponent,
} from "echarts/components";

let registered = false;

/** Register chart types needed for P0–P2. Call once at app boot. */
export function registerEcharts(): void {
  if (registered) {
    return;
  }
  use([
    CanvasRenderer,
    BarChart,
    HeatmapChart,
    LineChart,
    ScatterChart,
    DataZoomComponent,
    GridComponent,
    LegendComponent,
    MarkLineComponent,
    TooltipComponent,
    VisualMapComponent,
  ]);
  registered = true;
}

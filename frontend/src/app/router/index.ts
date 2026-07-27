import { createRouter, createWebHistory } from "vue-router";

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      redirect: "/market",
    },
    {
      path: "/market",
      name: "market-overview",
      component: () =>
        import("@/features/market-overview/pages/MarketOverviewPage.vue"),
    },
    {
      path: "/market/scanner",
      name: "market-scanner",
      component: () =>
        import("@/features/market-scanner/pages/MarketScannerPage.vue"),
    },
    {
      path: "/market/analysis/foreign-flow",
      name: "foreign-flow-analysis",
      component: () =>
        import(
          "@/features/cross-sectional-analysis/pages/ForeignFlowAnalysisPage.vue"
        ),
    },
    {
      path: "/stocks/:stockCode",
      name: "stock-detail",
      component: () =>
        import("@/features/stock-detail/pages/StockDetailPage.vue"),
      props: true,
    },
    {
      path: "/:pathMatch(.*)*",
      redirect: "/market",
    },
  ],
  scrollBehavior() {
    return { top: 0 };
  },
});

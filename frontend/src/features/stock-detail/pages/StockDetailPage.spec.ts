import { flushPromises, mount } from "@vue/test-utils";
import { defineComponent, nextTick, ref } from "vue";
import { createMemoryHistory, createRouter } from "vue-router";
import { afterEach, describe, expect, it, vi } from "vitest";
import StockDetailPage from "./StockDetailPage.vue";

vi.mock("../composables/useNetValueTrendQuery", () => ({
  useNetValueTrendQuery: () => queryResult(),
}));
vi.mock("../composables/useInvestorNetFlowQuery", () => ({
  useInvestorNetFlowQuery: () => queryResult(),
}));
vi.mock("../composables/useForeignGrossFlowQuery", () => ({
  useForeignGrossFlowQuery: () => queryResult(),
}));
vi.mock("../composables/useCumulativeNetValueQuery", () => ({
  useCumulativeNetValueQuery: () => queryResult(),
}));

function queryResult() {
  return {
    chartModel: ref(null),
    isPending: ref(false),
    isFetching: ref(false),
    errorMessage: ref(null),
    refetch: vi.fn(),
  };
}

describe("StockDetailPage", () => {
  afterEach(() => {
    vi.useRealTimers();
  });

  it("preserves a route stock change when a date changes before debounce settles", async () => {
    vi.useFakeTimers();
    const router = createRouter({
      history: createMemoryHistory(),
      routes: [
        {
          path: "/stocks/:stockCode",
          name: "stock-detail",
          component: StockDetailPage,
        },
        { path: "/market", component: { template: "<div />" } },
      ],
    });
    await router.push({
      name: "stock-detail",
      params: { stockCode: "BBCA" },
      query: { start_date: "2026-07-01", end_date: "2026-08-01" },
    });

    const wrapper = mount(StockDetailPage, {
      global: {
        plugins: [router],
        stubs: {
          PageHeader: { template: "<div><slot /></div>" },
          FilterBar: { template: "<div><slot /></div>" },
          StockCodeSelect: true,
          DateRangeFilter: defineComponent({
            emits: ["update:startDate"],
            template: '<button @click="$emit(\'update:startDate\', \'2026-07-02\')">date</button>',
          }),
          DashboardGrid: { template: "<div><slot /></div>" },
          NetValueTrendChart: true,
          InvestorNetFlowChart: true,
          ForeignGrossFlowChart: true,
          CumulativeNetValueChart: true,
        },
      },
    });

    await router.push({
      name: "stock-detail",
      params: { stockCode: "TLKM" },
      query: router.currentRoute.value.query,
    });
    await nextTick();
    await wrapper.get("button").trigger("click");
    await flushPromises();

    expect(router.currentRoute.value.params.stockCode).toBe("TLKM");
    expect(router.currentRoute.value.query.start_date).toBe("2026-07-02");

    await vi.advanceTimersByTimeAsync(300);
    await flushPromises();
    expect(router.currentRoute.value.params.stockCode).toBe("TLKM");

    wrapper.unmount();
  });
});

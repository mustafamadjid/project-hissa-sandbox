import { effectScope, ref } from "vue";
import { afterEach, describe, expect, it, vi } from "vitest";
import { useDebouncedRef } from "./useDebouncedRef";

describe("useDebouncedRef", () => {
  afterEach(() => {
    vi.useRealTimers();
  });

  it("updates only after delay and cancels stale updates", () => {
    vi.useFakeTimers();
    const source = ref("BBCA");
    const debounced = useDebouncedRef(source, 300);

    expect(debounced.value).toBe("BBCA");
    source.value = "BBRI";
    vi.advanceTimersByTime(299);
    expect(debounced.value).toBe("BBCA");

    source.value = "TLKM";
    vi.advanceTimersByTime(299);
    expect(debounced.value).toBe("BBCA");
    vi.advanceTimersByTime(1);
    expect(debounced.value).toBe("TLKM");
  });

  it("cancels pending update when scope stops", () => {
    vi.useFakeTimers();
    const source = ref("BBCA");
    const scope = effectScope();
    const debounced = scope.run(() => useDebouncedRef(source, 300));

    expect(debounced).toBeDefined();
    source.value = "BBRI";
    scope.stop();
    vi.advanceTimersByTime(300);
    expect(debounced?.value).toBe("BBCA");
  });
});

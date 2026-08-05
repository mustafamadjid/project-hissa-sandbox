import {
  onScopeDispose,
  ref,
  shallowReadonly,
  toValue,
  watch,
  type MaybeRefOrGetter,
  type Ref,
} from "vue";

export function useDebouncedRef<T>(
  source: MaybeRefOrGetter<T>,
  delay: number,
): Readonly<Ref<T>> {
  const value = ref(toValue(source)) as Ref<T>;
  let timer: ReturnType<typeof setTimeout> | undefined;

  watch(
    () => toValue(source),
    (nextValue) => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        value.value = nextValue;
      }, delay);
    },
    { flush: "sync" },
  );

  onScopeDispose(() => clearTimeout(timer), true);
  return shallowReadonly(value);
}

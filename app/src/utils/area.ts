import type { Area } from "@/api/common-area/types"

export const AREAS = [
  { label: 'Padel', value: 'padel' },
  { label: 'Gymnasio', value: 'gym' },
  { label: 'Piscina', value: 'pool' },
] as const


export const getAreaLabel = (area: Area) => {
  return AREAS.find((a) => a.value === area)?.label
}

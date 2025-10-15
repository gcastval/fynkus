import { formatDate } from '@/utils/date'
import { $fetch } from '../config'
import type { GetReservationRequest, PostReservationRequest, ScheduleResponse } from './types'

export const getReservations = async (request: GetReservationRequest) => {
  const date = formatDate(new Date(request.date))
  const endpoint = `api/v1/common-area/schedule/${request.area}/${date}`

  return await $fetch<ScheduleResponse>({ endpoint })
}

export const reserveHour = async (request: PostReservationRequest) => {
  const endpoint = `api/v1/common-area/reserve`

  const body = {
    area: request.area,
    date: formatDate(new Date(request.date)),
    hour: request.hour,
  }

  const [error] = await $fetch<void>({ endpoint, body, method: 'POST' })

  return error === null
}

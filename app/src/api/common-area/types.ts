export interface GetReservationRequest {
  area: string
  date: Date
}

export interface PostReservationRequest extends GetReservationRequest {
  hour: number
}

export interface ScheduleResponse {
  hours: Hour[]
}

export type Hour = {
  hour: number
  reserved: boolean
}

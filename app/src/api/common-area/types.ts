import {  AREAS } from "@/utils/area"

export interface GetReservationRequest {
  area: Area
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

export type Area = typeof AREAS[number]['value']

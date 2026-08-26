
variable "task_definition" {
  type = any
}

variable "private_subnets_ids" {
  type = list(string)
}

variable "backend_tg_arn" {
  type = string
}

variable "vpc_id" {
  type = string
}

variable "alb_sg_id" {
  type = string
}
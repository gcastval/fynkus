
variable "vpc_id" {
  type = string
}

variable "acm_arn" {
  type = string
}

variable "public_subnets_ids" {
  type = list(string)
}
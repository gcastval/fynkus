

variable "vpc_name" {
  type = string
  description = "Name of the VPC"
}


variable "cidr_block" {
  type = string
  description = "CIDR block for the VPC"
}

variable "availability_zones" {
  type = list(string)
  default = ["eu-west-1a", "eu-west-1b"]
  description = "List of availability zones"

  validation {
    condition = length(var.availability_zones) == 2
    error_message = "The list of availability zones must have exactly 2 elements."
  }
}

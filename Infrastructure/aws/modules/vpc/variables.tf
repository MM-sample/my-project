variable "system" {
  type = string
}

variable "env" {
  type = string
}

variable "region" {
  type = string
}

variable "vpc_values" {
  type = map(any)
}

variable "cidr_block_public" {
  type = map(string)
}

variable "cidr_block_private" {
  type = map(string)
}

variable "nat_gateway" {
}

variable "map_public_ip_on_launch_public_subnet" {
  type = bool
}

variable "map_public_ip_on_launch_private_subnet" {
  type = bool
}

variable "endpoints" {
}

variable "security_group" {
}
#----------------------------------------------------------------------------------------------------
# VPC
#----------------------------------------------------------------------------------------------------
resource "aws_vpc" "main" {
  cidr_block                       = var.vpc_values.cidr_block
  instance_tenancy                 = var.vpc_values.instance_tenancy # VPC内インスタンスのテナント属性を指定
  enable_dns_support               = var.vpc_values.dns_support      # VPC内でDNSによる名前解決を有効化するかを指定
  enable_dns_hostnames             = var.vpc_values.dns_hostname     # VPC内インスタンスがDNSホスト名を取得するかを指定
  assign_generated_ipv6_cidr_block = var.vpc_values.ipv6_cidr_block  # IPv6を有効化するかを指定

  tags = {
    Name = "${var.system}-${var.env}-vpc"
  }
}

#----------------------------------------------------------------------------------------------------
# Subnet Public
#----------------------------------------------------------------------------------------------------
resource "aws_subnet" "public" {
  for_each                = var.cidr_block_public
  vpc_id                  = aws_vpc.main.id
  cidr_block              = each.value
  map_public_ip_on_launch = var.map_public_ip_on_launch_public_subnet
  availability_zone       = data.aws_availability_zones.available.names[each.key]

  tags = {
    Name = "${var.system}-${var.env}-public-${each.key + 1}"
  }
}

#----------------------------------------------------------------------------------------------------
# Subnet Private
#----------------------------------------------------------------------------------------------------
resource "aws_subnet" "private" {
  for_each                = var.cidr_block_private
  vpc_id                  = aws_vpc.main.id
  cidr_block              = each.value
  map_public_ip_on_launch = var.map_public_ip_on_launch_private_subnet
  availability_zone       = data.aws_availability_zones.available.names[each.key]

  tags = {
    Name = "${var.system}-${var.env}-private-${each.key + 1}"
  }
}
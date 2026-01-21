#----------------------------------------------------------------------------------------------------
# Internet Gateway
#----------------------------------------------------------------------------------------------------
resource "aws_internet_gateway" "igw" {
  vpc_id = aws_vpc.main.id

  tags = {
    Name = "${var.system}-${var.env}-igw"
  }
}

#----------------------------------------------------------------------------------------------------
# Nat Gateway
#----------------------------------------------------------------------------------------------------
resource "aws_eip" "eip" {
  for_each = var.nat_gateway.create_resource ? var.nat_gateway.private_cidr : {}
  domain   = "vpc" # VPC内でEIPを使用（vpc = trueの代わりに）

  tags = {
    Name = "${var.system}-${var.env}-eip${each.key}"
  }
}

resource "aws_nat_gateway" "ngw" {
  for_each      = var.nat_gateway.create_resource ? var.nat_gateway.private_cidr : {}
  subnet_id     = aws_subnet.public[each.key].id # ここは汎用的にしたいね
  allocation_id = aws_eip.eip[each.key].id

  tags = {
    Name = "${var.system}-${var.env}-ngw${each.key}"
  }
}
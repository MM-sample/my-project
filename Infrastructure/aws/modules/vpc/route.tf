#----------------------------------------------------------------------------------------------------
# Route Table
#----------------------------------------------------------------------------------------------------
resource "aws_route_table" "public_rt" {
  vpc_id = aws_vpc.main.id

  tags = {
    Name = "${var.system}-${var.env}-public-rt"
  }
}

resource "aws_route_table" "private_rt" {
  for_each = var.cidr_block_private
  vpc_id   = aws_vpc.main.id

  tags = {
    Name = "${var.system}-${var.env}-private-rt-${each.key}"
  }
}

#----------------------------------------------------------------------------------------------------
# Route Table Association
#----------------------------------------------------------------------------------------------------
resource "aws_route_table_association" "public_rt_assoc" {
  for_each       = var.cidr_block_public
  subnet_id      = aws_subnet.public[each.key].id
  route_table_id = aws_route_table.public_rt.id
}

resource "aws_route_table_association" "private_rt_assoc" {
  for_each       = var.cidr_block_private
  subnet_id      = aws_subnet.private[each.key].id
  route_table_id = aws_route_table.private_rt[each.key].id
}

#----------------------------------------------------------------------------------------------------
# Route
#----------------------------------------------------------------------------------------------------
resource "aws_route" "igw_r" {
  destination_cidr_block = "0.0.0.0/0"
  route_table_id         = aws_route_table.public_rt.id
  gateway_id             = aws_internet_gateway.igw.id
}

resource "aws_route" "nat_gateway_r" {
  for_each = var.nat_gateway.create_resource ? var.nat_gateway.private_cidr : {}
  destination_cidr_block = "0.0.0.0/0"
  route_table_id = aws_route_table.private_rt[each.key].id
  nat_gateway_id = aws_nat_gateway.ngw[each.key].id

  depends_on = [ aws_nat_gateway.ngw ]
}
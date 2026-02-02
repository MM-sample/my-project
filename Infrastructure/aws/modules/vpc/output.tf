
output "aws_vpc" {
  value = aws_vpc.main
}

output "aws_subnet_public" {
  value = aws_subnet.public
}

output "aws_subnet_private" {
  value = aws_subnet.private
}

output "aws_availability_zones_names" {
  value = data.aws_availability_zones.available.names
}

output "route_table_private_rt" {
  value = aws_route_table.private_rt
}

output "aws_security_group_vpc_endpoint" {
  value = aws_security_group.vpc_endpoint
}

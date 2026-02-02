
#----------------------------------------------------------------------------------------------------
# Endpoint Gateway
#----------------------------------------------------------------------------------------------------
resource "aws_vpc_endpoint" "gateway" {
  for_each          = var.endpoints.gateway.create_resource ? var.endpoints.gateway.names: {}

  vpc_id            = aws_vpc.main.id
  service_name      = "com.amazonaws.${var.region}.${each.value}"
  vpc_endpoint_type = "Gateway"

  route_table_ids   = var.endpoints.gateway.route_table_ids
}

#----------------------------------------------------------------------------------------------------
# Endpoint Interface
#----------------------------------------------------------------------------------------------------
resource "aws_vpc_endpoint" "interface" {
  for_each            = var.endpoints.interface.create_resource ? var.endpoints.interface.names : {}

  vpc_id              = aws_vpc.main.id
  #TODO:: ドメイン設定が必要か確認！！！
  service_name        = "com.amazonaws.${var.region}.${each.value}"
  vpc_endpoint_type   = "Interface"
  private_dns_enabled = true

  subnet_ids          = var.endpoints.interface.subnet_ids
  security_group_ids  = var.endpoints.interface.security_group_ids
}

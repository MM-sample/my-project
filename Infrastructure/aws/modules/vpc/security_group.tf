
resource "aws_security_group" "vpc_endpoint" {
  name          = "${var.security_group.endpoint.name}"
  description   = "${var.security_group.endpoint.name}"

  vpc_id        = aws_vpc.main.id

  dynamic "ingress" {
    for_each = var.security_group.endpoint.ingress

    content {
      from_port       = ingress.value.from_port
      to_port         = ingress.value.to_port
      protocol        = ingress.value.protocol
      self            = try(ingress.value.self, false)
      cidr_blocks     = try(ingress.value.cidr_blocks, [])
      security_groups = try(ingress.value.security_groups, [])
    }
  }

  dynamic "egress" {
    for_each = var.security_group.endpoint.egress

    content {
      from_port   = egress.value.from_port
      to_port     = egress.value.to_port
      protocol    = egress.value.protocol
      cidr_blocks = egress.value.cidr_blocks
    }
  }

  tags = {
    Name = "${var.system}-${var.env}-${var.security_group.endpoint.name}"
  }
}
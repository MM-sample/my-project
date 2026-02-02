
resource "aws_security_group" "ec2_http_sg" {

  count = var.security_group.create_resource ? 1 : 0

  name   = "ec2-${var.security_group.http.name}-sg"
  vpc_id = var.security_group.http.vpc_id

  dynamic "ingress" {
    for_each = var.security_group.http.ingress

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
    for_each = var.security_group.http.egress

    content {
      from_port   = egress.value.from_port
      to_port     = egress.value.to_port
      protocol    = egress.value.protocol
      cidr_blocks = egress.value.cidr_blocks
    }
  }

  tags = {
    Name = "ec2-${var.security_group.http.name}-sg"
  }
}

resource "aws_security_group" "ec2_ssh_sg" {

  count = var.security_group.create_resource ? 1 : 0

  name   = "ec2-${var.security_group.ssh.name}-sg"
  vpc_id = var.security_group.ssh.vpc_id

  dynamic "ingress" {
    for_each = var.security_group.ssh.ingress

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
    for_each = var.security_group.ssh.egress

    content {
      from_port   = egress.value.from_port
      to_port     = egress.value.to_port
      protocol    = egress.value.protocol
      cidr_blocks = egress.value.cidr_blocks
    }
  }

  tags = {
    Name = "ec2-${var.security_group.ssh.name}-sg"
  }
}
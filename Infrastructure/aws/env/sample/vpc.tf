locals {
  network = {
    vpc = {
      cidr_block = "10.1.0.0/16"
      instance_tenancy = "default"
      dns_support = true
      dns_hostname = true
      ipv6_cidr_block = false
    }

    subnet  = {
      public  = { 0 = "10.1.1.0/24", 1 = "10.1.2.0/24", }
      private = { 0 = "10.1.101.0/24", 1 = "10.1.102.0/24",}
    }
  }

  nat_gateway = {
    create_resource = false #今回は使用しない
    public_cidr     = { 0 = "10.1.1.0/24",   1 = "10.1.2.0/24",}
    private_cidr    = { 0 = "10.1.101.0/24", 1 = "10.1.102.0/24",}
  }

  #配置するEC2に対して「publicIP」を設定 true:有効 false:無効
  mapPublicIpOnLaunch = {
    public  = true
    private = false
  }

  endpoints = {
    gateway = {
      create_resource = true
      route_table_ids = [ for rt in module.vpc.route_table_private_rt : rt.id ]
      names = {
        0 = "s3"
#       1 = "dynamodb"  #dynamodbを使いたいなら
      }
    }
    interface = {
      create_resource = false
      subnet_ids         = [ for sn in module.vpc.aws_subnet_private : sn.id ]
      security_group_ids = [ module.vpc.aws_security_group_vpc_endpoint.id ]
      names = {
#       0 = "s3"
#       1 = "dynamodb"  #dynamodbを使いたいなら
      }
    }
  }

  vpc_security_group = {
    endpoint = {
      name = "vpc_endpoint"
      ingress = [{
        description     = "HTTPS from VPC"
        from_port       = 443
        to_port         = 443
        protocol        = "tcp"
        cidr_blocks     = ["10.1.0.0/16"] # VPC全体を許可
      }]
      egress = [{
        from_port = 0
        to_port   = 0
        protocol    = "-1"
        cidr_blocks = ["0.0.0.0/0"]
      }]
    }
  }
}

module "vpc" {
  source                                 = "../../modules/vpc"
  env                                    = var.env
  system                                 = var.system
  region                                 = var.region
  vpc_values                             = local.network.vpc
  cidr_block_public                      = local.network.subnet.public
  cidr_block_private                     = local.network.subnet.private
  nat_gateway                            = local.nat_gateway
  map_public_ip_on_launch_public_subnet  = local.mapPublicIpOnLaunch.public
  map_public_ip_on_launch_private_subnet = local.mapPublicIpOnLaunch.private
  endpoints                              = local.endpoints
  security_group                         = local.vpc_security_group
}

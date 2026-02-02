locals {

  base_ec2_create_source = true
  keypair_filepath       = "../../modules/ec2/files/keypair.pub"
  user_data_filepath     = "../../modules/ec2/files/sh/userdata.sh"

  ec2_data = {
    iam = {
      create_resource = local.base_ec2_create_source
    }
    ami = {
      most_recent = true               #true: 最新を取得
      owners      = ["self", "amazon"] #自身が登録したものと　amazon公式
      filters = {
        0 = { name = "name",                             value = ["al2023-ami-kernel-6.1-x86_64"] }
        1 = { name = "root-device-type",                 value = ["ebs"] }
        2 = { name = "virtualization-type",              value = ["hvm"] }
        3 = { name = "architecture",                     value = ["x86_64"] }
        4 = { name = "block-device-mapping.volume-type", value = ["gp2"] }
      }
    }
  }

  ec2_security_group = {
    create_resource = local.base_ec2_create_source
    http = {
      name   = "http"
      vpc_id = module.vpc.aws_vpc.id
      ingress = [
      {
        from_port   = 80
        to_port     = 80
        protocol    = "tcp"
        # ALBからのトラフィックのみ許可
        # security_groups = compact([
        #   try(module.LB.aws_security_group_alb_sg.id, null)
        # ])
        cidr_blocks = ["0.0.0.0/0"]
      }]
      egress = [{
        from_port = 0
        to_port   = 0
        protocol  = "-1"
        cidr_blocks = ["0.0.0.0/0"]
      }]
    }
    ssh = {
      name   = "ssh"
      vpc_id = module.vpc.aws_vpc.id
      ingress = [{
        from_port   = 22
        to_port     = 22
        protocol    = "tcp"
        # 自分のグローバルIPアドレスに置き換えてください
        cidr_blocks = ["0.0.0.0/0"]
        security_groups =[]
      }]
      egress = [{
        from_port   = 0
        to_port     = 0
        protocol    = "-1"
        cidr_blocks = ["0.0.0.0/0"]
      }]
    }
  }

  keypair = {
    create_resource = local.base_ec2_create_source
    file_path       = local.keypair_filepath
  }

  instance = {
    create_resource             = local.base_ec2_create_source
    instance_type               = "t3.micro"
    subnet_ids                  = module.vpc.aws_subnet_public
    vpc_security_group_ids      = compact([
      try(module.ec2.aws_security_group_ec2_http.id, null),
      try(module.ec2.aws_security_group_ec2_ssh.id,  null)
    ])
    associate_public_ip_address = true
    iam_instance_profile        = try(module.ec2.aws_iam_instance_profile_ec2_instance_profile.name, "")
    key_pair                    = try(module.ec2.aws_key_pair_ec2_keypair.key_name, null)
    tags_type                   = "app-ec2"
    user_data                   = fileexists(local.user_data_filepath) ? file(local.user_data_filepath) : null
    metadata_options = {
      http_tokens = "required"        # IMDSv2 トークンベース認証 クロスサイトスクリプティング (XSS) 攻撃やその他の攻撃からの保護強化
      http_put_response_hop_limit = 1 # EC2 インスタンス内でプロキシなどを経由して IMDS にアクセスをする場合 設定は2にする　(範囲 1〜64)
    }
  }
}

module ec2 {
  source         = "../../modules/ec2"
  env            = var.env
  system         = var.system
  iam            = local.ec2_data.iam
  ami_data       = local.ec2_data.ami
  keypair        = local.keypair
  security_group = local.ec2_security_group
  instance       = local.instance
}

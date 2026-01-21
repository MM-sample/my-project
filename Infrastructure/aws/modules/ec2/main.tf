#----------------------------------------------------------------------------------------------------
# AWS EC2 Instance
#----------------------------------------------------------------------------------------------------
resource "aws_instance" "main" {
  for_each = var.instance.create_resource ? var.instance.subnet_ids : {}

  ami           = data.aws_ami.ec2_ami.id
  instance_type = var.instance.instance_type

  subnet_id                   = each.value.id
  vpc_security_group_ids      = var.instance.vpc_security_group_ids
  associate_public_ip_address = var.instance.associate_public_ip_address
  iam_instance_profile        = var.instance.iam_instance_profile

  key_name  = var.instance.key_pair
  user_data = var.instance.user_data

  metadata_options {
    http_tokens                 = var.instance.metadata_options.http_tokens
    http_put_response_hop_limit = var.instance.metadata_options.http_put_response_hop_limit
  }

  tags = {
    Name = "${var.system}-${var.env}-ec2-instance-${each.key + 1}"
    Project = var.system
    Env     = var.env
    Type    = var.instance.tags_type
  }

#   # ここがポイント！
#   # インターネットへの「出口」が完成してから、インスタンスの作成（とスクリプト実行）を開始する
#   depends_on = [
#     var.gateway_ids
#   ]
}
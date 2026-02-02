#----------------------------------------------------------------------------------------------------
# IAM
#----------------------------------------------------------------------------------------------------
data "aws_iam_policy_document" "ec2_policy" {
  statement {
    effect = "Allow"
    actions = ["sts:AssumeRole"]

    principals {
      type = "Service"
      identifiers = ["ec2.amazonaws.com"]
    }
  }
}

#----------------------------------------------------------------------------------------------------
# ami *最新のEC2を取得
#----------------------------------------------------------------------------------------------------
data aws_ami "ec2_ami" {
  most_recent = var.ami_data.most_recent
  owners      = var.ami_data.owners

  dynamic "filter" {
    for_each = var.ami_data.filters

    content {
      name   = filter.value.name
      values = filter.value.value
    }
  }
}

#デバック
output "iam_ec2_policy_document_json" {
  value = data.aws_iam_policy_document.ec2_policy.json
}

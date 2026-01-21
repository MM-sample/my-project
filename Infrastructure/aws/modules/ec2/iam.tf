#----------------------------------------------------------------------------------------------------
# IAM Role
#----------------------------------------------------------------------------------------------------
resource "aws_iam_role" "ec2_iam_role" {

  count = var.iam.create_resource ? 1 : 0

  name = "${var.system}-${var.env}-ec2-iam-role"
  assume_role_policy = data.aws_iam_policy_document.ec2_policy.json
}

#----------------------------------------------------------------------------------------------------
# IAM Policy Attachment
#----------------------------------------------------------------------------------------------------
resource "aws_iam_role_policy_attachment" "ec2_iam_role_attachment" {
  for_each = var.iam.create_resource ? {
    EC2ReadOnlyAccess        = "arn:aws:iam::aws:policy/AmazonEC2ReadOnlyAccess"
    SSManageInstanceCore     = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
    SSMReadOnlyAccess        = "arn:aws:iam::aws:policy/AmazonSSMReadOnlyAccess"
    S3FullAccess             = "arn:aws:iam::aws:policy/AmazonS3FullAccess"
    CloudWatchLogsFullAccess = "arn:aws:iam::aws:policy/CloudWatchLogsFullAccess"
#    S3ReadOnlyAccess         = "arn:aws:iam::aws:policy/AmazonS3ReadOnlyAccess"
  } : {}

  role       = aws_iam_role.ec2_iam_role[0].name
  policy_arn = each.value
}

#----------------------------------------------------------------------------------------------------
# Instance Profile ＠EC2の紐付けに必要
#----------------------------------------------------------------------------------------------------
resource "aws_iam_instance_profile" "ec2_iam_instance_profile" {
  count = var.iam.create_resource ? 1 : 0

  name  = "${var.system}-${var.env}-ec2-instance-profile"
  role  = aws_iam_role.ec2_iam_role[count.index].name
}

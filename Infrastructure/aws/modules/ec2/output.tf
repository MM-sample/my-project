
output "aws_instance_main" {
  value = aws_instance.main
}

output "aws_security_group_ec2_http" {
  value = try(aws_security_group.ec2_http_sg[0], null)
}

output "aws_security_group_ec2_ssh" {
  value = try(aws_security_group.ec2_ssh_sg[0], null)
}

output "aws_iam_instance_profile_ec2_instance_profile" {
 value = try(aws_iam_instance_profile.ec2_iam_instance_profile[0], null)
}

output "aws_key_pair_ec2_keypair" {
  value = try(aws_key_pair.ec2_keypair[0], null)
}
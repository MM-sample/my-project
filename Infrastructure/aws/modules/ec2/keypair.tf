#----------------------------------------------------------------------------------------------------
# Key Pair
#----------------------------------------------------------------------------------------------------
resource "aws_key_pair" "ec2_keypair" {
  count = var.keypair.create_resource ? 1 : 0

  key_name = "${var.system}-${var.env}-keypair"
  public_key = file(var.keypair.file_path)

  tags = {
    Name    = "${var.system}-${var.env}-keypair"
    Project = var.system
    Env     = var.env
  }
}

terraform {

 required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "6.61.0"
    }
  }

  required_version = "1.15.8"
}

provider "aws" {
  region = var.region
}

module "vpc" {
  source = "./modules/01_vpc"
  vpc_name = "main-vpc"
  cidr_block = "10.0.0.0/16"
  availability_zones = ["eu-west-1a", "eu-west-1b"]
}

module "acm" {
  source = "./modules/02_acm"
  domain_name = "fynkus-api.gcastillo.site"
}

module "alb" { 
  source = "./modules/03_alb"

  vpc_id = module.vpc.vpc_id
  acm_arn = module.acm.acm_validated_arn
  public_subnets_ids = module.vpc.public_subnets_ids
}

module "ecs" {
  source = "./modules/04_ecs"

  task_definition = jsondecode(file("../aws-cli/task/task-definition.json"))
  private_subnets_ids = module.vpc.private_subnets_ids
  backend_tg_arn = module.alb.api_tg_arn
  vpc_id = module.vpc.vpc_id
  alb_sg_id = module.alb.alb_security_group_id

  depends_on = [module.alb]
}
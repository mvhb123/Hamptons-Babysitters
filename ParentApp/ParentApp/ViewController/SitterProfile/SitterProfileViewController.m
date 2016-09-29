//
//  SitterProfileViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 30/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "SitterProfileViewController.h"

@interface SitterProfileViewController ()

@end

@implementation SitterProfileViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
     self.navigationItem.title = @"Sitter Profile";
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    NSMutableDictionary *dict_sitterProfileData = [[NSMutableDictionary alloc]init];
    [dict_sitterProfileData setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_sitterProfileData setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_sitterProfileData setSafeObject:self.str_sitterId forKey:@"sitter_id"];
    [dict_sitterProfileData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kSitterDetailAPI];
    DDLogInfo(@"%@",kSitterDetailAPI);
    networkManager.delegate = self;
    [networkManager sitterDetail:dict_sitterProfileData forRequestNumber:1];

    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    contentHight = 0;
    UIView *lLast = [view_sitterProfile.subviews lastObject];
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht;
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+20);
    
}

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/
//-(void)setSetterDetailInView
//{
//    self.sitterInfo = [ApplicationManager getInstance].sitterInfo;
//     NSURL *img_url=[NSURL URLWithString:self.sitterInfo.thumbImage];
//    [view_sitterImage loadImageFromURL:img_url];
//    lbl_sitterName.text = [NSString stringWithFormat:@"%@ %@",self.sitterInfo.sitterFirstName,self.sitterInfo.sitterLastName];
//    txtView_aboutMe.text = self.sitterInfo.aboutMe;
//    lbl_phone1.text = self.sitterInfo.phone;
//    lbl_phone2.text = self.sitterInfo.sitterLocalPhone;
//    lbl_email.text =  self.sitterInfo.sitterUserName;
//    if ([self.sitterInfo.infantTraining isEqualToString:@"yes"]) {
//        lbl_certificationInfant.text = [NSString stringWithFormat:@"%@,%@",self.sitterInfo.infantTraining,self.sitterInfo.infantTrainingDate];
//    }
//    else
//    {
//        lbl_certificationInfant.text = self.sitterInfo.infantTraining;
//    }
//    if ([self.sitterInfo.cprAdult isEqualToString:@"yes"]) {
//       lbl_cprcertificationAdult.text= [NSString stringWithFormat:@"%@,%@",self.sitterInfo.cprAdult,self.sitterInfo.cprAdultDate];
//    }
//    else
//    {
//        lbl_cprcertificationAdult.text = self.sitterInfo.cprAdult;
//    }
//    if ([self.sitterInfo.waterCertification isEqualToString:@"yes"]) {
//        lbl_waterCertification.text = [NSString stringWithFormat:@"%@,%@",self.sitterInfo.waterCertification,self.sitterInfo.waterCertDate];
//    }
//    else
//    {
//        lbl_waterCertification.text = self.sitterInfo.waterCertification;
//    }
//    lbl_education.text = self.sitterInfo.education;
//    lbl_langauge.text = [[self.sitterInfo.array_preferences safeObjectAtIndex:0]safeObjectForKey:kPreferName];
//    
//}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
               // [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
                self.parentInfo.tokenData = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kTokenData];
                dict_sitterDetail = [dict_responseObj mutableCopy];
                [[ApplicationManager getInstance]saveSitterDetail:dict_responseObj];
               // [self setSetterDetailInView];
               
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
        case 6:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
                NSArray *array = [self.navigationController viewControllers];
                
                [self.navigationController popToViewController:[array objectAtIndex:0] animated:YES];
                
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;

    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    DDLogInfo(@"%@",error);
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription]];
    
}


@end

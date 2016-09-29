//
//  GuardianViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 15/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "NumberFormatter.h"

@interface GuardianViewController : AppBaseViewController
{
    __weak IBOutlet UILabel *lbl_emerPhone1;
    __weak IBOutlet UILabel *lbl_emerRelationship;
    __weak IBOutlet UILabel *lbl_emerName;
    __weak IBOutlet UILabel *lbl_emergencyContactHeader;
    __weak IBOutlet UILabel *lbl_gurdPhone2;
    __weak IBOutlet UILabel *lbl_gurdPhone1;
    __weak IBOutlet UILabel *lbl_gurdRelationship;
    __weak IBOutlet UILabel *lbl_gurdName;
    __weak IBOutlet UILabel *lbl_gurdianHeader;
    __weak IBOutlet UIView *view_otherGurdian;
    __weak IBOutlet UITextField *txt_firstName;
    __weak IBOutlet UITextField *txt_phone2;
    __weak IBOutlet UITextField *txt_phone1;
    __weak IBOutlet UITextField *txt_relationship;
    __weak IBOutlet UITextField *txt_emergencyContactPhone1;
    __weak IBOutlet UITextField *txt_emergencyContactRelationship;
    __weak IBOutlet UITextField *txt_emergencyContactNAme;
    int checkData;
     float contentHight;
}
@property (nonatomic ,strong) NumberFormatter *numFormatter;
@property(nonatomic)int checkValue;
@property(nonatomic)int CheckFirstTimeEdit;
@property (nonatomic,strong)NSMutableDictionary *dict_updateProfileData;
@property (nonatomic,strong)NSMutableDictionary *dict_loginData;
@property (nonatomic,strong)NSMutableDictionary *dict_savedUpdatedData;

@end

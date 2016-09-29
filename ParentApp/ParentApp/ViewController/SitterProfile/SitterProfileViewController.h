//
//  SitterProfileViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 30/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"

@interface SitterProfileViewController : AppBaseViewController
{
    __weak IBOutlet UIView *view_sitterProfile;
    __weak IBOutlet AsyncImageView *view_sitterImage;
    __weak IBOutlet UILabel *lbl_sitterName;
    __weak IBOutlet UITextView *txtView_aboutMe;
    __weak IBOutlet UILabel *lbl_phone1;
    __weak IBOutlet UILabel *lbl_phone2;
    __weak IBOutlet UILabel *lbl_email;
    
    __weak IBOutlet UILabel *lbl_langauge;
    __weak IBOutlet UILabel *lbl_education;
    __weak IBOutlet UILabel *lbl_cprcertificationAdult;
    __weak IBOutlet UILabel *lbl_otherCertification;
    __weak IBOutlet UILabel *lbl_waterCertification;
    __weak IBOutlet UILabel *lbl_certificationInfant;
    NSMutableDictionary *dict_sitterDetail;
    float contentHight;
}
@property(nonatomic,strong)NSString *str_sitterId;
@property(nonatomic,assign)Parent *parentInfo;
@property(nonatomic,assign)Sitter *sitterInfo;

@end

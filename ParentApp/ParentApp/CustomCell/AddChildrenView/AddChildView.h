//
//  AddChildView.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 06/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface AddChildView : UIView
@property (weak, nonatomic) IBOutlet UIButton *btn_previous;
@property (weak, nonatomic) IBOutlet UIButton *btn_next;
@property (weak, nonatomic) IBOutlet AsyncImageView *view_childImage;
@property (weak, nonatomic) IBOutlet UILabel *lbl_childName;
@property (weak, nonatomic) IBOutlet UILabel *lbl_age;
@property (weak, nonatomic) IBOutlet UILabel *lbl_ageValue;
@property (weak, nonatomic) IBOutlet UILabel *lbl_sex;
@property (weak, nonatomic) IBOutlet UILabel *lbl_sexValue;


@end
